import type { ReferenceAsset } from '../../assets/landingAssets';
import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';
import type {
  ChatMessage,
  ChatOwner,
  ChatPet,
  Conversation,
} from './messaging.types';

type PaginationMeta = {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};

type PaginatedEnvelope<T> = {
  success: true;
  message: string;
  data: T[];
  meta: PaginationMeta;
  links: {
    first: string;
    last: string;
    prev: string | null;
    next: string | null;
  };
};

type RaceApiItem = {
  id: number;
  species_id: number;
  name: string;
};

type ParticipantPetApiItem = {
  id: number;
  user_id: number;
  name: string;
  species_id: number;
  race: RaceApiItem | null;
  age_years: number;
  sex: number | null;
  images: string[];
};

type ParticipantApiItem = {
  user_id: number;
  name: string | null;
  pet: ParticipantPetApiItem | null;
};

type MessageApiItem = {
  id: number;
  conversation_id: number;
  sender_user_id: number;
  receiver_user_id: number;
  content: string;
  is_seen: boolean;
  created_at: string;
  updated_at: string;
};

type ConversationApiItem = {
  id: number;
  current_user_id: number;
  participants: ParticipantApiItem[];
  last_message: MessageApiItem | null;
  unread_count: number;
};

function isPositiveInteger(value: number): boolean {
  return Number.isInteger(value) && value > 0;
}

function photoForPet(
  pet: ParticipantPetApiItem,
): ReferenceAsset {
  const path = pet.images.find(
    image =>
      typeof image === 'string'
      && image.trim() !== '',
  );

  return {
    src: path ? mediaUrl(path) : null,
    alt: path
      ? `${pet.name} pet photo`
      : `${pet.name} pet profile`,
    placeholder: pet.name,
  };
}

function mapPet(
  pet: ParticipantPetApiItem,
): ChatPet {
  if (
    !isPositiveInteger(pet.id)
    || !isPositiveInteger(pet.user_id)
    || typeof pet.name !== 'string'
    || pet.name.trim() === ''
    || !Number.isInteger(pet.age_years)
    || pet.age_years < 0
    || !Array.isArray(pet.images)
  ) {
    throw new Error(
      'Messaging participant Pet response is invalid.',
    );
  }

  const sex =
    pet.sex === 1
      ? 'female'
      : pet.sex === 0
        ? 'male'
        : undefined;

  return {
    id: String(pet.id),
    name: pet.name,
    photo: photoForPet(pet),
    breed: pet.race?.name,
    ageYears: pet.age_years,
    sex,
  };
}

function mapParticipant(
  participant: ParticipantApiItem,
): {
  owner: ChatOwner;
  pet: ChatPet;
} | null {
  if (
    !isPositiveInteger(participant.user_id)
    || participant.pet === null
    || participant.pet.user_id
      !== participant.user_id
  ) {
    return null;
  }

  const pet = mapPet(participant.pet);

  return {
    owner: {
      id: String(participant.user_id),
      name: participant.name,
      representedPetId: pet.id,
    },
    pet,
  };
}

function validateMessage(
  message: MessageApiItem,
  expectedConversationId: number,
  currentUserId: number,
  otherUserId: number,
): MessageApiItem {
  const validDirection =
    (
      message.sender_user_id === currentUserId
      && message.receiver_user_id
        === otherUserId
    )
    || (
      message.sender_user_id === otherUserId
      && message.receiver_user_id
        === currentUserId
    );

  if (
    !isPositiveInteger(message.id)
    || message.conversation_id
      !== expectedConversationId
    || !validDirection
    || typeof message.content !== 'string'
    || typeof message.created_at !== 'string'
  ) {
    throw new Error(
      'Messaging thread response does not preserve User ID semantics.',
    );
  }

  return message;
}

function mapMessage(
  message: MessageApiItem,
): ChatMessage {
  return {
    id: String(message.id),
    senderId: String(message.sender_user_id),
    content: message.content,
    timestamp: message.created_at,
    receipt: message.is_seen
      ? 'read'
      : undefined,
  };
}

function mapConversation(
  item: ConversationApiItem,
  expectedCurrentUserId: number,
): Conversation | null {
  if (
    !isPositiveInteger(item.id)
    || item.current_user_id
      !== expectedCurrentUserId
    || !Array.isArray(item.participants)
    || item.participants.length !== 2
    || !Number.isInteger(item.unread_count)
    || item.unread_count < 0
  ) {
    throw new Error(
      'Messaging conversation response is invalid.',
    );
  }

  const mapped = item.participants
    .map(mapParticipant);

  if (
    mapped.some(participant => participant === null)
  ) {
    return null;
  }

  const participants = mapped as [
    NonNullable<(typeof mapped)[number]>,
    NonNullable<(typeof mapped)[number]>,
  ];

  const currentOwner = participants.find(
    participant =>
      participant.owner.id
      === String(expectedCurrentUserId),
  );

  const otherOwner = participants.find(
    participant =>
      participant.owner.id
      !== String(expectedCurrentUserId),
  );

  if (!currentOwner || !otherOwner) {
    throw new Error(
      'Messaging conversation participants do not include the authenticated User.',
    );
  }

  if (item.last_message) {
    validateMessage(
      item.last_message,
      item.id,
      expectedCurrentUserId,
      Number(otherOwner.owner.id),
    );
  }

  return {
    id: String(item.id),
    pets: [
      currentOwner.pet,
      otherOwner.pet,
    ],
    owners: [
      currentOwner.owner,
      otherOwner.owner,
    ],
    currentOwnerId:
      String(expectedCurrentUserId),
    preview:
      item.last_message?.content
      ?? 'No messages yet',
    lastActivityAt:
      item.last_message?.created_at
      ?? null,
    unreadCount: item.unread_count,
    messages: [],
    interests: [],
  };
}

export async function conversationListRequest({
  token,
  currentUserId,
}: {
  token: string;
  currentUserId: number;
}): Promise<Conversation[]> {
  if (!isPositiveInteger(currentUserId)) {
    throw new Error(
      'Authenticated User ID is required to load conversations.',
    );
  }

  const response = await apiRequest<
    PaginatedEnvelope<ConversationApiItem>
  >(
    '/conversations?per_page=50',
    { token },
  );

  return response.data
    .map(item =>
      mapConversation(
        item,
        currentUserId,
      ),
    )
    .filter(
      (
        conversation,
      ): conversation is Conversation =>
        conversation !== null,
    );
}

export async function messageThreadRequest({
  token,
  conversationId,
  currentUserId,
  receiverUserId,
}: {
  token: string;
  conversationId: number;
  currentUserId: number;
  receiverUserId: number;
}): Promise<ChatMessage[]> {
  if (
    !isPositiveInteger(conversationId)
    || !isPositiveInteger(currentUserId)
    || !isPositiveInteger(receiverUserId)
    || currentUserId === receiverUserId
  ) {
    throw new Error(
      'Valid Messaging conversation and User IDs are required.',
    );
  }

  const response = await apiRequest<
    PaginatedEnvelope<MessageApiItem>
  >(
    `/messages?receiver_id=${receiverUserId}&per_page=50&page=1`,
    { token },
  );

  return response.data.map(item =>
    mapMessage(
      validateMessage(
        item,
        conversationId,
        currentUserId,
        receiverUserId,
      ),
    ),
  );
}
