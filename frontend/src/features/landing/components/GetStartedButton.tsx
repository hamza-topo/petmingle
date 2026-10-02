import { ChevronRight, PawPrint } from 'lucide-react';
import { ActionLink } from '../../../components/Action';

export function GetStartedButton() {
  return (
    <ActionLink className="get-started" to="/pet/create" variant="primary">
      <PawPrint size={30} fill="currentColor" aria-hidden="true" />
      Get Started
      <ChevronRight size={25} aria-hidden="true" />
    </ActionLink>
  );
}
