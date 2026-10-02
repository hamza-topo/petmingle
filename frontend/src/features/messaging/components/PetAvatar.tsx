import { Avatar } from '../../../components/Avatar';
import type { ChatPet } from '../messaging.fixtures';
export function PetAvatar({ pet }: { pet: ChatPet }) { return <Avatar className="chat-pet-avatar" asset={pet.photo} />; }
export function PetPairAvatar({ pets }: { pets: [ChatPet, ChatPet] }) {
  return <span className="chat-pair-avatar"><PetAvatar pet={pets[0]} /><PetAvatar pet={pets[1]} /></span>;
}
