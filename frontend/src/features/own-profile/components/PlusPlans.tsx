import { ArrowUpRight } from 'lucide-react';

export function PlusPlans() {
  return (
    <aside id="petmingle-plus" className="own-plus" aria-labelledby="own-plus-title">
      <span className="plus-coming">À venir</span>
      <h2 id="own-plus-title">PetMingle <strong>Plus</strong></h2>
      <p className="own-plus-intro">Encore plus de possibilités pour leurs prochaines rencontres.</p>
      <details className="plus-preview">
        <summary>Découvrir la direction de Plus <ArrowUpRight size={18} aria-hidden="true" /></summary>
        <p>Nous préparons des filtres plus précis et de nouvelles façons de découvrir les animaux près de vous. Les fonctionnalités et les tarifs seront annoncés à leur lancement.</p>
      </details>
      <p className="own-cancel">Les abonnements ne sont pas encore disponibles.</p>
    </aside>
  );
}
