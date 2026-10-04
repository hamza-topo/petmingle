import {
  render,
  screen,
  waitFor,
} from '@testing-library/react';
import userEvent from '@testing-library/user-event';
import { MemoryRouter } from 'react-router';
import {
  afterEach,
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { App } from '../../app/App';
import { useAuth } from '../../auth/AuthProvider';
import { tokenStorage } from '../../auth/tokenStorage';
import { authenticatedAuthState } from '../../test/authFixtures';
import { PetCreatePage } from './PetCreatePage';
import { taxonomyRequest } from './taxonomy.api';

vi.mock('../../auth/AuthProvider', () => ({
  useAuth: vi.fn(),
}));

vi.mock('./taxonomy.api', () => ({
  taxonomyRequest: vi.fn(),
}));

const mockedUseAuth = vi.mocked(useAuth);
const mockedTaxonomyRequest = vi.mocked(taxonomyRequest);

beforeEach(() => {
  mockedUseAuth.mockReturnValue(
    authenticatedAuthState(),
  );

  tokenStorage.set('test-token');

  mockedTaxonomyRequest.mockReset();

  mockedTaxonomyRequest.mockResolvedValue({
    species: [
      {
        id: 10,
        name: 'Dog',
        description: 'Dogs',
      },
      {
        id: 11,
        name: 'Cat',
        description: 'Cats',
      },
    ],
    races: [
      {
        id: 20,
        species_id: 10,
        name: 'Golden Retriever',
      },
      {
        id: 21,
        species_id: 10,
        name: 'Labrador Retriever',
      },
      {
        id: 30,
        species_id: 11,
        name: 'Domestic Shorthair',
      },
    ],
  });

  vi.stubGlobal(
    'URL',
    Object.assign(URL, {
      createObjectURL: vi.fn(
        () => 'blob:pet-preview',
      ),
      revokeObjectURL: vi.fn(),
    }),
  );
});

afterEach(() => {
  tokenStorage.clear();
  vi.unstubAllGlobals();
});

function renderForm() {
  return render(
    <MemoryRouter
      initialEntries={['/pet/create']}
    >
      <App />
    </MemoryRouter>,
  );
}

async function selectDogTaxonomy(
  user: ReturnType<typeof userEvent.setup>,
) {
  await screen.findByRole('option', {
    name: 'Dog',
  });

  await user.selectOptions(
    screen.getByRole('combobox', {
      name: /Species/i,
    }),
    '10',
  );

  await screen.findByRole('option', {
    name: 'Golden Retriever',
  });

  await user.selectOptions(
    screen.getByRole('combobox', {
      name: /Breed/i,
    }),
    '20',
  );
}

describe('Pet profile creation', () => {
  it('renders its route, sections and first progress step', async () => {
    renderForm();

    expect(
      screen.getByRole('heading', {
        level: 1,
        name: /Let’s find/,
      }),
    ).toBeVisible();

    for (const name of [
      'Add a photo',
      'Basic information',
      'Personality',
      'Playdate preferences',
    ]) {
      expect(
        screen.getByRole('region', { name }),
      ).toBeVisible();
    }

    expect(
      screen
        .getByText('Pet Info')
        .closest('li'),
    ).toHaveAttribute(
      'aria-current',
      'step',
    );

    expect(
      screen.getByRole('link', {
        name: 'Home',
      }),
    ).not.toHaveAttribute('aria-current');

    expect(
      screen.getByRole('textbox', {
        name: 'Pet name',
      }),
    ).toHaveValue('Nala');

    expect(
      await screen.findByRole('option', {
        name: 'Dog',
      }),
    ).toBeInTheDocument();
  });

  it('loads taxonomy using the authenticated token', async () => {
    renderForm();

    await screen.findByRole('option', {
      name: 'Dog',
    });

    expect(
      mockedTaxonomyRequest,
    ).toHaveBeenCalledTimes(1);

    expect(
      mockedTaxonomyRequest,
    ).toHaveBeenCalledWith(
      'test-token',
    );
  });

  it('loads species and filters races by selected species', async () => {
    const user = userEvent.setup();

    renderForm();

    expect(
      await screen.findByRole('option', {
        name: 'Dog',
      }),
    ).toBeInTheDocument();

    expect(
      screen.getByRole('option', {
        name: 'Cat',
      }),
    ).toBeInTheDocument();

    expect(
      screen.getByRole('combobox', {
        name: /Breed/i,
      }),
    ).toBeDisabled();

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: /Species/i,
      }),
      '10',
    );

    expect(
      await screen.findByRole('option', {
        name: 'Golden Retriever',
      }),
    ).toBeInTheDocument();

    expect(
      screen.getByRole('option', {
        name: 'Labrador Retriever',
      }),
    ).toBeInTheDocument();

    expect(
      screen.queryByRole('option', {
        name: 'Domestic Shorthair',
      }),
    ).not.toBeInTheDocument();

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: /Species/i,
      }),
      '11',
    );

    expect(
      await screen.findByRole('option', {
        name: 'Domestic Shorthair',
      }),
    ).toBeInTheDocument();

    expect(
      screen.queryByRole('option', {
        name: 'Golden Retriever',
      }),
    ).not.toBeInTheDocument();
  });

  it('clears the selected race when species changes', async () => {
    const user = userEvent.setup();

    renderForm();

    await selectDogTaxonomy(user);

    expect(
      screen.getByRole('combobox', {
        name: /Breed/i,
      }),
    ).toHaveValue('20');

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: /Species/i,
      }),
      '11',
    );

    await waitFor(() => {
      expect(
        screen.getByRole('combobox', {
          name: /Breed/i,
        }),
      ).toHaveValue('');
    });
  });

  it('requires a nonblank pet name before local submission', async () => {
    const user = userEvent.setup();

    renderForm();

    await user.clear(
      screen.getByRole('textbox', {
        name: 'Pet name',
      }),
    );

    await user.type(
      screen.getByRole('textbox', {
        name: 'Pet name',
      }),
      '   ',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Continue',
      }),
    );

    expect(
      await screen.findByText(
        'Enter your pet’s name.',
      ),
    ).toBeInTheDocument();

    expect(
      screen.queryByRole('status'),
    ).not.toBeInTheDocument();
  });

  it('submits API taxonomy IDs with selected traits and preferences', async () => {
    const user = userEvent.setup();
    const submit = vi.fn();

    render(
      <MemoryRouter>
        <PetCreatePage
          onLocalSubmit={submit}
        />
      </MemoryRouter>,
    );

    await selectDogTaxonomy(user);

    expect(
      screen.getByRole('checkbox', {
        name: 'Playful',
      }),
    ).toBeChecked();

    await user.click(
      screen.getByRole('checkbox', {
        name: 'Friendly',
      }),
    );

    await user.click(
      screen.getByRole('checkbox', {
        name: 'Playful',
      }),
    );

    await user.selectOptions(
      screen.getByLabelText(
        'Energy level',
      ),
      'Low energy',
    );

    await user.selectOptions(
      screen.getByLabelText(
        'Ideal playdate type',
      ),
      'Gentle play',
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Continue',
      }),
    );

    await waitFor(() => {
      expect(
        submit,
      ).toHaveBeenCalledWith(
        expect.objectContaining({
          name: 'Nala',
          speciesId: '10',
          raceId: '20',
          traits: ['Friendly'],
          energy: 'Low energy',
          playdate: 'Gentle play',
          photo: null,
        }),
      );
    });

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent(
      'Pet details are valid.',
    );
  });

  it('previews, replaces and removes a local photo, releasing object URLs', async () => {
    const user = userEvent.setup();

    const { unmount } = renderForm();

    const file = new File(
      ['photo'],
      'pet.png',
      {
        type: 'image/png',
      },
    );

    await user.upload(
      screen.getByLabelText('Pet photo'),
      file,
    );

    expect(
      await screen.findByRole('img', {
        name: 'Selected pet photo',
      }),
    ).toHaveAttribute(
      'src',
      'blob:pet-preview',
    );

    await user.upload(
      screen.getByLabelText('Pet photo'),
      new File(
        ['another'],
        'pet2.jpg',
        {
          type: 'image/jpeg',
        },
      ),
    );

    expect(
      URL.revokeObjectURL,
    ).toHaveBeenCalledTimes(1);

    await user.click(
      screen.getByRole('button', {
        name: 'Remove photo',
      }),
    );

    expect(
      screen.queryByRole('img', {
        name: 'Selected pet photo',
      }),
    ).not.toBeInTheDocument();

    expect(
      URL.revokeObjectURL,
    ).toHaveBeenCalledTimes(2);

    await user.upload(
      screen.getByLabelText('Pet photo'),
      file,
    );

    unmount();

    expect(
      URL.revokeObjectURL,
    ).toHaveBeenCalledTimes(3);
  });

  it('rejects unsupported and oversized photos without submitting', async () => {
    const user = userEvent.setup({
      applyAccept: false,
    });

    renderForm();

    await user.upload(
      screen.getByLabelText('Pet photo'),
      new File(
        ['gif'],
        'pet.gif',
        {
          type: 'image/gif',
        },
      ),
    );

    await user.click(
      screen.getByRole('button', {
        name: 'Continue',
      }),
    );

    expect(
      await screen.findByText(
        'Choose a JPG or PNG image.',
      ),
    ).toBeInTheDocument();

    const large = new File(
      ['png'],
      'large.png',
      {
        type: 'image/png',
      },
    );

    Object.defineProperty(
      large,
      'size',
      {
        value:
          10 * 1024 * 1024 + 1,
      },
    );

    await user.upload(
      screen.getByLabelText('Pet photo'),
      large,
    );

    expect(
      await screen.findByText(
        /no larger than 10MB/,
      ),
    ).toBeInTheDocument();

    expect(
      screen.queryByRole('status'),
    ).not.toBeInTheDocument();
  });

  it('shows a loading state while taxonomy is being fetched', async () => {
    let resolveTaxonomy:
      | ((value: {
        species: [];
        races: [];
      }) => void)
      | undefined;

    mockedTaxonomyRequest.mockImplementationOnce(
      () =>
        new Promise(resolve => {
          resolveTaxonomy = resolve;
        }),
    );

    renderForm();

    expect(
      screen.getByRole('status'),
    ).toHaveTextContent(
      'Loading pet taxonomy...',
    );

    expect(
      screen.getByRole('combobox', {
        name: /Species/i,
      }),
    ).toBeDisabled();

    resolveTaxonomy?.({
      species: [],
      races: [],
    });

    expect(
      await screen.findByText(
        'No species are available.',
      ),
    ).toBeInTheDocument();
  });

  it('shows a taxonomy error and allows retrying the request', async () => {
    const user = userEvent.setup();

    mockedTaxonomyRequest
      .mockRejectedValueOnce(
        new Error('Network error'),
      )
      .mockResolvedValueOnce({
        species: [
          {
            id: 10,
            name: 'Dog',
            description: 'Dogs',
          },
        ],
        races: [
          {
            id: 20,
            species_id: 10,
            name: 'Golden Retriever',
          },
        ],
      });

    renderForm();

    expect(
      await screen.findByRole('alert'),
    ).toHaveTextContent(
      'Unable to load species and breeds.',
    );

    expect(
      screen.getByRole('combobox', {
        name: /Species/i,
      }),
    ).toBeDisabled();

    await user.click(
      screen.getByRole('button', {
        name: 'Try again',
      }),
    );

    expect(
      await screen.findByRole('option', {
        name: 'Dog',
      }),
    ).toBeInTheDocument();

    expect(
      mockedTaxonomyRequest,
    ).toHaveBeenCalledTimes(2);
  });

  it('shows an empty state when no species are available', async () => {
    mockedTaxonomyRequest.mockResolvedValueOnce({
      species: [],
      races: [],
    });

    renderForm();

    expect(
      await screen.findByText(
        'No species are available.',
      ),
    ).toBeInTheDocument();

    expect(
      screen.getByRole('button', {
        name: 'Continue',
      }),
    ).toBeDisabled();
  });

  it('shows an empty breed state when the selected species has no races', async () => {
    const user = userEvent.setup();

    mockedTaxonomyRequest.mockResolvedValueOnce({
      species: [
        {
          id: 99,
          name: 'Rabbit',
          description: null,
        },
      ],
      races: [],
    });

    renderForm();

    await screen.findByRole('option', {
      name: 'Rabbit',
    });

    await user.selectOptions(
      screen.getByRole('combobox', {
        name: /Species/i,
      }),
      '99',
    );

    expect(
      await screen.findByText(
        'No breeds are available for the selected species.',
      ),
    ).toBeInTheDocument();
  });
});