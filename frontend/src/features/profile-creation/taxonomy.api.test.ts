import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import {
  racesRequest,
  speciesRequest,
  taxonomyRequest,
} from './taxonomy.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

describe('taxonomy API', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('loads species through the authenticated API client', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of species.',
      data: [
        {
          id: 10,
          name: 'Dog',
          description: 'Dogs',
        },
      ],
    });

    await expect(
      speciesRequest('test-token'),
    ).resolves.toEqual([
      {
        id: 10,
        name: 'Dog',
        description: 'Dogs',
      },
    ]);

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/species',
      {
        method: 'GET',
        token: 'test-token',
      },
    );
  });

  it('loads races through the authenticated API client', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of races.',
      data: [
        {
          id: 20,
          species_id: 10,
          name: 'Golden Retriever',
        },
      ],
    });

    await expect(
      racesRequest('test-token'),
    ).resolves.toEqual([
      {
        id: 20,
        species_id: 10,
        name: 'Golden Retriever',
      },
    ]);

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/races',
      {
        method: 'GET',
        token: 'test-token',
      },
    );
  });

  it('loads species and races as one taxonomy result', async () => {
    mockedApiRequest
      .mockResolvedValueOnce({
        success: true,
        message: 'List of species.',
        data: [
          {
            id: 10,
            name: 'Dog',
            description: 'Dogs',
          },
        ],
      })
      .mockResolvedValueOnce({
        success: true,
        message: 'List of races.',
        data: [
          {
            id: 20,
            species_id: 10,
            name: 'Golden Retriever',
          },
        ],
      });

    await expect(
      taxonomyRequest('test-token'),
    ).resolves.toEqual({
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

    expect(mockedApiRequest).toHaveBeenCalledTimes(2);
  });

  it('propagates taxonomy API failures', async () => {
    mockedApiRequest.mockRejectedValueOnce(
      new Error('Unable to load taxonomy.'),
    );

    await expect(
      taxonomyRequest('test-token'),
    ).rejects.toThrow(
      'Unable to load taxonomy.',
    );
  });
});