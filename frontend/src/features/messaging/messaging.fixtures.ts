import { nalaIdentity } from '../../fixtures/petIdentity';
import type { ReferenceAsset } from '../../assets/landingAssets';

export interface ChatPet { id: string; name: string; photo: ReferenceAsset; breed?: string; ageYears?: number; sex?: 'female' | 'male' }
export interface ChatOwner { id: string; name: string | null; representedPetId: string }
export interface ChatMessage { id: string; senderId: string; content: string; timestamp: string; receipt?: 'read' }
export interface Conversation {
  id: string; pets: [ChatPet, ChatPet]; owners: [ChatOwner, ChatOwner]; currentOwnerId: string;
  matchedOn?: string; preview: string; activityLabel: string; unreadCount: number;
  messages: ChatMessage[]; interests: { id: string; label: string; artwork: ReferenceAsset }[];
}
export const placeholder = (label: string): ReferenceAsset => ({ src: null, alt: label, placeholder: label });
const pairs = [
  ['Nala', 'Milo', 'Sounds perfect! Saturday...', '9:39 AM'],
  ['Luna', 'Toby', 'Let’s meet at the dog park!', 'Yesterday'],
  ['Simba', 'Mochi', 'Mochi would love to play!', 'Yesterday'],
  ['Buddy', 'Bella', 'We had such a great time!', 'Mon'],
  ['Daisy', 'Coco', 'Are you free this weekend?', 'Mon'],
  ['Charlie', 'Olive', 'That sounds great!', 'Sun'],
  ['Max', 'Willow', 'Willow can’t wait to meet!', 'Sun'],
  ['Rocky', 'Zoe', 'Thanks for the tips!', 'Apr 12'],
];
export const conversations: Conversation[] = pairs.map(([first, second, preview, activityLabel], index) => {
  const id = first.toLowerCase();
  const pets: [ChatPet, ChatPet] = [
    { id: `${id}-pet-a`, name: first, photo: placeholder(first) },
    { id: `${id}-pet-b`, name: second, photo: placeholder(second) },
  ];
  // Account identities are intentionally anonymous. The reference only names pets.
  const owners: [ChatOwner, ChatOwner] = [
    { id: `${id}-owner-a`, name: null, representedPetId: pets[0].id },
    { id: 'current-owner', name: null, representedPetId: pets[1].id },
  ];
  return { id, pets, owners, currentOwnerId: 'current-owner', preview, activityLabel, unreadCount: index === 0 ? 1 : 0,
    messages: [{ id: `${id}-preview`, senderId: owners[0].id, content: preview, timestamp: `${index < 3 || index === 7 ? '2024-04-12' : index < 5 ? '2024-04-08' : '2024-04-07'}T09:30:00` }], interests: [] };
});
const initial = conversations[0];
Object.assign(initial.pets[0], { breed: nalaIdentity.breed, ageYears: nalaIdentity.ageYears, sex: 'female' });
Object.assign(initial.pets[1], { breed: 'Pembroke Welsh Corgi', ageYears: 2, sex: 'male' });
initial.matchedOn = '2024-03-28';
initial.messages = [
  { id: 'nala-1', senderId: initial.owners[0].id, content: 'Hi! Nala would love to meet Milo!', timestamp: '2024-04-13T09:34:00' },
  { id: 'milo-1', senderId: initial.currentOwnerId, content: "That's great! Milo is always up for a new playmate. 🐾", timestamp: '2024-04-13T09:36:00', receipt: 'read' },
  { id: 'nala-2', senderId: initial.owners[0].id, content: "How about a walk at Balboa Park this weekend? It's one of Nala’s favorite spots!", timestamp: '2024-04-13T09:38:00' },
  { id: 'milo-2', senderId: initial.currentOwnerId, content: 'Sounds perfect! Saturday morning works for us. 🙂', timestamp: '2024-04-13T09:39:00', receipt: 'read' },
];
initial.interests = [
  { id: 'park', label: 'Park adventures', artwork: placeholder('Park artwork') },
  { id: 'fetch', label: 'Playtime & fetch', artwork: placeholder('Fetch artwork') },
  { id: 'outdoor', label: 'Outdoor exploration', artwork: placeholder('Outdoor artwork') },
];
export const messagingAccount = { photo: placeholder('Owner'), location: 'San Diego, CA' };
export const pairName = (conversation: Conversation) => conversation.pets.map(pet => pet.name).join(' & ');
export const messageTime = (timestamp: string) => new Date(timestamp).toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });

// Relative labels are anchored to the frozen reference conversation date.
export function messageDay(timestamp: string) {
  const date = timestamp.slice(0, 10);
  if (date === '2024-04-13') return 'Today';
  if (date === '2024-04-12') return 'Yesterday';
  return new Date(timestamp).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
}
