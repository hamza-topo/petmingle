import { ArrowRight } from 'lucide-react';
import { Link } from 'react-router';
import { PetCard } from '../../../components/PetCard';
import { SectionHeading } from '../../../components/SectionHeading';
import { featuredPets } from '../landing.fixtures';

export function FeaturedPets() {
  return (
    <section id="featured-pets" aria-labelledby="featured-heading" className="featured-pets">
      <SectionHeading
        id="featured-heading"
        description="Real pets. Real people. Happier days."
        action={
          <Link className="see-more" to="/discover">
            See more pets <ArrowRight size={20} aria-hidden="true" />
          </Link>
        }
      >
        Featured Pets
      </SectionHeading>
      <ul className="featured-pet-grid" aria-label="Featured pets">
        {featuredPets.map((pet) => <li key={pet.id}><PetCard pet={pet} /></li>)}
      </ul>
    </section>
  );
}
