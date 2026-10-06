import type { ReferenceAsset } from '../../assets/landingAssets';
import { apiRequest } from '../../api/client';
import { mediaUrl } from '../../api/config';
import type {
  ChatMessage,
  ChatOwner,
  ChatPet,
  Conversation,
} from './messaging.types';

type PageMeta = {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
};

type PageEnvelope<T> = {
  success: true;
  message: string;
  data: T[];
  meta: PageMeta;
  links: {
    first: string | null;
    last: string | null;
    prev: string | null;
    next: string | null;
  };
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

type CreatedMessageApiItem = {
  id: number;
  conversation_id: number;
  sender_id: number;
  receiver_id: number;
  content: string;
  is_seen?: boolean | number | null;
  created_at: string;
  updated_at: string;
};

type ApiEnvelope<T> = {
  success: true;
  message: string;
  data: T;
};

type ParticipantApiItem = {
  user_id: number;
  name: string | null;
  pet: {
    id: number;
    user_id: number;
    name: string;
    species_id: number;
    race: {
      id: number;
      species_id: number;
      name: string;
    } | null;
    age_years: number;
    sex: number | null;
    images: string[];
  } | null;
};

type ConversationApiItem = {
  id: number;
  current_user_id: number;
  participants: [ParticipantApiItem, ParticipantApiItem];
  last_message: MessageApiItem | null;
  unread_count: number;
};

function isPositiveInteger(value: number): boolean {
  return Number.isInteger(value) && value > 0;
}

function photoFor(
  name: string,
  images: string[],
): ReferenceAsset {
  const path = images.find(
    image =>
      typeof image === 'string'
      && image.trim() !== '',
  );

  return {
    src: path ? mediaUrl(path) : null,
    alt: path ? `${name} pet photo` : `${name} pet profile`,
    placeholder: name,
  };
}

function mapPet(
  participant: ParticipantApiItem,
): ChatPet {
  const pet = participant.pet;

  if (
    !pet
    || !isPositiveInteger(pet.id)
    || pet.user_id !== participant.user_id
    || typeof pet.name !== 'string'
    || pet.name.trim() === ''
    || !Array.isArray(pet.images)
  ) {
    throw new Error(
      'Messaging participant is missing a valid persisted Pet identity.',
    );
  }

  return {
    id: String(pet.id),
    name: pet.name,
    photo: photoFor(pet.name, pet.images),
    breed: pet.race?.name,
    ageYears: Number.isInteger(pet.age_years)
      ? pet.age_years
      : undefined,
    sex: pet.sex === 1
      ? 'female'
      : pet.sex === 0
        ? 'male'
        : undefined,
  };
}

function mapOwner(
  participant: ParticipantApiItem,
  pet: ChatPet,
): ChatOwner {
  if (!isPositiveInteger(participant.user_id)) {
    throw new Error(
      'Messaging participant does not preserve User ID semantics.',
    );
  }

  return {
    id: String(participant.user_id),
    name: participant.name,
    representedPetId: pet.id,
  };
}

function mapMessage(
  item: MessageApiItem,
): ChatMessage {
  if (
    !isPositiveInteger(item.id)
    || !isPositiveInteger(item.conversation_id)
    || !isPositiveInteger(item.sender_user_id)
    || !isPositiveInteger(item.receiver_user_id)
    || typeof item.content !== 'string'
    || typeof item.created_at !== 'string'
  ) {
    throw new Error('Messaging thread response is invalid.');
  }

  return {
    id: String(item.id),
    senderId: String(item.sender_user_id),
    content: item.content,
    timestamp: item.created_at,
    receipt:
      item.is_seen === true || item.is_seen === 1
        ? 'read'
        : undefined,
  };
}

function mapCreatedMessage(
  item: CreatedMessageApiItem,
  {
    conversationId,
    currentUserId,
    receiverUserId,
  }: {
    conversationId: number;
    currentUserId: number;
    receiverUserId: number;
  },
): ChatMessage {
  if (
    !isPositiveInteger(item.id)
    || item.conversation_id !== conversationId
    || item.sender_id !== currentUserId
    || item.receiver_id !== receiverUserId
    || typeof item.content !== 'string'
    || typeof item.created_at !== 'string'
  ) {
    throw new Error(
      'Created message response does not preserve authenticated conversation identity.',
    );
  }

  return {
    id: String(item.id),
    senderId: String(item.sender_id),
    content: item.content,
    timestamp: item.created_at,
    receipt: item.is_seen ? 'read' : undefined,
  };
}

export function messageTime(timestamp: string): string {
  return new Date(timestamp).toLocaleTimeString(
    'en-US',
    {
      hour: 'numeric',
      minute: '2-digit',
    },
  );
}

export function messageDay(timestamp: string): string {
  const messageDate = new Date(timestamp);
  const today = new Date();

  if (
    messageDate.toDateString()
    === today.toDateString()
  ) {
    return 'Today';
  }

  const yesterday = new Date(today);
  yesterday.setDate(today.getDate() - 1);

  if (
    messageDate.toDateString()
    === yesterday.toDateString()
  ) {
    return 'Yesterday';
  }

  return messageDate.toLocaleDateString(
    'en-US',
    {
      month: 'short',
      day: 'numeric',
      year: 'numeric',
    },
  );
}

export function pairName(
  conversation: Conversation,
): string {
  return conversation.pets
    .map(pet => pet.name)
    .join(' & ');
}

export function mapConversation(
  item: ConversationApiItem,
): Conversation {
  if (
    !isPositiveInteger(item.id)
    || !isPositiveInteger(item.current_user_id)
    || item.participants.length !== 2
    || !Number.isInteger(item.unread_count)
    || item.unread_count < 0
  ) {
    throw new Error(
      'Messaging conversation response is invalid.',
    );
  }

  const firstPet = mapPet(item.participants[0]);
  const secondPet = mapPet(item.participants[1]);

  const firstOwner = mapOwner(
    item.participants[0],
    firstPet,
  );
  const secondOwner = mapOwner(
    item.participants[1],
    secondPet,
  );

  if (
    ![
      Number(firstOwner.id),
      Number(secondOwner.id),
    ].includes(item.current_user_id)
  ) {
    throw new Error(
      'Messaging conversation does not include the authenticated User.',
    );
  }

  return {
    id: String(item.id),
    pets: [firstPet, secondPet],
    owners: [firstOwner, secondOwner],
    currentOwnerId: String(item.current_user_id),
    preview: item.last_message?.content ?? '',
    activityLabel: item.last_message
      ? messageTime(item.last_message.created_at)
      : '',
    unreadCount: item.unread_count,
    messages: item.last_message
      ? [mapMessage(item.last_message)]
      : [],
    interests: [],
  };
}

export async function conversationsRequest(
  token: string,
): Promise<Conversation[]> {
  const response = await apiRequest<
    PageEnvelope<ConversationApiItem>
  >(
    '/conversations?per_page=50',
    {
      method: 'GET',
      token,
    },
  );

  return response.data.map(mapConversation);
}

export async function threadRequest({
  token,
  receiverUserId,
}: {
  token: string;
  receiverUserId: number;
}): Promise<ChatMessage[]> {
  const response = await apiRequest<
    PageEnvelope<MessageApiItem>
  >(
    `/messages?receiver_id=${receiverUserId}&per_page=50&page=1`,
    {
      method: 'GET',
      token,
    },
  );

  return response.data.map(mapMessage);
}

export async function messageSendRequest({
  token,
  conversationId,
  currentUserId,
  receiverUserId,
  content,
}: {
  token: string;
  conversationId: number;
  currentUserId: number;
  receiverUserId: number;
  content: string;
}): Promise<ChatMessage> {
  if (
    !isPositiveInteger(conversationId)
    || !isPositiveInteger(currentUserId)
    || !isPositiveInteger(receiverUserId)
    || currentUserId === receiverUserId
  ) {
    throw new Error(
      'Message send request contains invalid conversation participants.',
    );
  }

  const response = await apiRequest<
    ApiEnvelope<CreatedMessageApiItem>
  >(
    '/messages',
    {
      method: 'POST',
      token,
      body: JSON.stringify({
        receiver_id: receiverUserId,
        content,
      }),
    },
  );

  return mapCreatedMessage(
    response.data,
    {
      conversationId,
      currentUserId,
      receiverUserId,
    },
  );
}

export function otherParticipantUserId(
  conversation: Conversation,
): number {
  const other = conversation.owners.find(
    owner => owner.id !== conversation.currentOwnerId,
  );

  if (!other) {
    throw new Error(
      'Messaging conversation is missing the other participant.',
    );
  }

  return Number(other.id);
}
