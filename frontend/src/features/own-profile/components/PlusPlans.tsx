import { useState } from 'react';
import { Check, ChevronRight, Crown } from 'lucide-react';
import { ActionButton } from '../../../components/Action';
import { plusFeatures, plusPlans, type PlusPlan } from '../profile.fixtures';
function PlusPlanCard({ plan, selected, onSelect }: { plan: PlusPlan; selected: boolean; onSelect: () => void }) {
  return <label className="own-plan" data-selected={selected}>
    {plan.recommended && <span className="own-popular">Most Popular</span>}
    <span className="own-plan-name"><input type="radio" name="plus-plan" value={plan.id} checked={selected} onChange={onSelect} aria-label={plan.label} /><span>{plan.label}</span></span>
    <span className="own-plan-price"><strong>{plan.monthlyPrice}</strong><span> / month</span></span>
    <span className="own-plan-billing">{plan.billing}</span>
    <span className="own-saving-slot">{plan.saving && <span>{plan.saving}</span>}</span>
    <span className="own-plan-benefits">{plan.benefits.map(text => <span key={text}><Check size={17} aria-hidden="true" />{text}</span>)}</span>
  </label>;
}
export function PlusPlans() {
  const [selected, setSelected] = useState('annual');
  return <aside id="petmingle-plus" className="own-plus" aria-labelledby="own-plus-title">
    <h2 id="own-plus-title"><Crown size={52} fill="currentColor" aria-hidden="true" /><span>PetMingle <strong>Plus</strong></span></h2>
    <h3>More connections. More adventures.</h3>
    <p className="own-plus-intro">Unlock premium features and help Nala meet even more amazing friends. PetMingle Plus gives you everything you need to make meaningful connections.</p>
    <ul className="own-plus-features">{plusFeatures.map(({ title, text, icon: Icon }) => <li key={title}><span className="own-plus-icon"><Icon size={32} aria-hidden="true" /></span><div><h4>{title}</h4><p>{text}</p></div></li>)}</ul>
    <fieldset className="own-plans"><legend className="sr-only">Choose a PetMingle Plus plan</legend>{plusPlans.map(plan => <PlusPlanCard key={plan.id} plan={plan} selected={selected === plan.id} onSelect={() => setSelected(plan.id)} />)}</fieldset>
    <ActionButton className="own-upgrade" unavailableReason="Purchases are not available in this preview"><Crown size={37} fill="currentColor" />Try PetMingle Plus<ChevronRight size={27} /></ActionButton>
    <p className="own-cancel">Cancel anytime. No commitment.</p>
  </aside>;
}
