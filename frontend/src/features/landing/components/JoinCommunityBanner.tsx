import { landingAssets } from '../../../assets/landingAssets';
import { ReferenceImage } from '../../../components/ReferenceImage';
import { GetStartedButton } from './GetStartedButton';

export function JoinCommunityBanner() {
  return (
    <section className="join-banner" aria-labelledby="join-heading">
      <div className="join-copy">
        <h2 id="join-heading">More friends. Brighter days.</h2>
        <p>Join a community that celebrates the special bond between pets and people.</p>
      </div>
      <GetStartedButton />
      <ReferenceImage asset={landingAssets.highFive} className="high-five-artwork" />
    </section>
  );
}
