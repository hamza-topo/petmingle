/** Exact reference assets are not present in the repository.
 * Replace null with an imported, approved standalone asset; never import /design.
 */
export interface ReferenceAsset {
  src: string | null;
  alt: string;
  placeholder: string;
  position?: string;
}

export const landingAssets = {
  logo: {
    src: null,
    alt: 'PetMingle',
    placeholder: 'Logo',
  },
  hero: {
    src: null,
    alt: 'Nala the golden retriever and Mochi the cat outdoors',
    placeholder: 'Nala & Mochi · hero photo placeholder',
    position: 'center',
  },
  highFive: {
    src: null,
    alt: 'A dog’s paw and a person’s hand sharing a high five',
    placeholder: 'High-five photo placeholder',
  },
} satisfies Record<string, ReferenceAsset>;
