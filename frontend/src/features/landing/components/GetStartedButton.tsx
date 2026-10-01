import { ChevronRight, PawPrint } from 'lucide-react';
import { ActionButton } from '../../../components/Action';

export function GetStartedButton() {
  return (
    <ActionButton className="get-started" unavailableReason="Signup is not available in this preview">
      <PawPrint size={30} fill="currentColor" aria-hidden="true" />
      Get Started
      <ChevronRight size={25} aria-hidden="true" />
    </ActionButton>
  );
}
