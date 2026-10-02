import { ReferenceImage } from '../../../components/ReferenceImage';
const steps = [
  ['Pet Info', 'Add basic details about your pet'],
  ['Personality', 'Choose a few words that describe them'],
  ['Preferences', 'Help us find the best playdate matches'],
  ['Review', 'Take a quick look and finish up'],
];
export function ProfileProgress() {
  return <aside className="pet-progress" aria-label="Profile creation progress">
    <p className="pet-progress-eyebrow">Create pet profile</p>
    <h1>Let’s find<br />new friends!</h1>
    <p className="pet-progress-intro">Tell us about your pet so they can discover amazing playmates near you.</p>
    <ol>{steps.map(([label, description], index) => <li key={label} aria-current={index === 0 ? 'step' : undefined}>
      <span className="pet-step-number">{index + 1}</span><div><strong>{label}</strong><p>{description}</p></div>
    </li>)}</ol>
    <ReferenceImage className="pet-progress-art" asset={{ src: null, alt: 'Dog and cat together', placeholder: 'Pet illustration' }} />
  </aside>;
}
