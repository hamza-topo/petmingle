import type { ReferenceAsset } from '../../assets/landingAssets';

export type ChatPet = {
  id: string;
  name: string;
  photo: ReferenceAsset;
  breed?: string;
  ageYears?: number;
  sex?: 'female' | 'male';
};

export type ChatOwner = {
  id: string;
  name: string | null;
  representedPetId: string;
};

export type ChatMessage = {
  id: string;
  senderId: string;
  content: string;
  timestamp: string;
  receipt?: 'read';
};

export type Conversation = {
  id: string;
  pets: [ChatPet, ChatPet];
  owners: [ChatOwner, ChatOwner];
  currentOwnerId: string;
  preview: string;
  lastActivityAt: string | null;
  unreadCount: number;
  messages: ChatMessage[];
  interests: [];
};
