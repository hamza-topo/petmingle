import {
  afterEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { petCreateRequest } from './pet-create.api';

const createdPet = {
  id: 55,
  user_id: 10,
  species_id: 10,
  race_id: 20,
  name: 'Milo',
  age: 4,
  sexe: null,
  color: null,
  images: [],
  about: null,
};

afterEach(() => {
  vi.unstubAllGlobals();
});

describe('petCreateRequest', () => {
  it('submits only the supported persisted fields as multipart data', async () => {
    const fetchMock = vi.fn().mockResolvedValue(
      new Response(
        JSON.stringify({
          success: true,
          message: 'Pet has been created.',
          data: createdPet,
        }),
        {
          status: 201,
          headers: {
            'Content-Type': 'application/json',
          },
        },
      ),
    );

    vi.stubGlobal('fetch', fetchMock);

    const photo = new File(
      ['photo'],
      'milo.png',
      {
        type: 'image/png',
      },
    );

    await expect(
      petCreateRequest(
        {
          speciesId: 10,
          raceId: 20,
          name: '  Milo  ',
          age: 4,
          photo,
        },
        'test-token',
      ),
    ).resolves.toEqual(createdPet);

    expect(fetchMock).toHaveBeenCalledTimes(1);

    const [, request] = fetchMock.mock.calls[0] as [
      string,
      RequestInit,
    ];

    expect(request.method).toBe('POST');

    const headers = new Headers(request.headers);

    expect(headers.get('Authorization')).toBe(
      'Bearer test-token',
    );

    expect(headers.get('Content-Type')).toBeNull();

    const body = request.body as FormData;

    expect(body.get('species_id')).toBe('10');
    expect(body.get('race_id')).toBe('20');
    expect(body.get('name')).toBe('Milo');
    expect(body.get('age')).toBe('4');
    expect(body.get('image')).toBe(photo);

    expect(body.has('size')).toBe(false);
    expect(body.has('traits')).toBe(false);
    expect(body.has('energy')).toBe(false);
    expect(body.has('playdate')).toBe(false);
  });
});
