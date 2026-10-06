import type { ReferenceAsset } from '../../assets/landingAssets';

export interface ChatPet {
  id: string;
  name: string;
  photo: ReferenceAsset;
  breed?: string;
  ageYears?: number;
  sex?: 'female' | 'male';
}

export interface ChatOwner {
  id: string;
  name: string | null;
  representedPetId: string;
}

export interface ChatMessage {
  id: string;
  senderId: string;
  content: string;
  timestamp: string;
  receipt?: 'read';
}

export interface Conversation {
  id: string;
  pets: [ChatPet, ChatPet];
  owners: [ChatOwner, ChatOwner];
  currentOwnerId: string;
  matchedOn?: string;
  preview: string;
  activityLabel: string;
  unreadCount: number;
  messages: ChatMessage[];
  interests: {
    id: string;
    label: string;
    artwork: ReferenceAsset;
  }[];
}
