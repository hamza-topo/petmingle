import {
  beforeEach,
  describe,
  expect,
  it,
  vi,
} from 'vitest';

import { apiRequest } from '../../api/client';
import {
  accountLocationsRequest,
  createAccountLocationRequest,
  updateAccountLocationRequest,
} from './location.api';

vi.mock('../../api/client', () => ({
  apiRequest: vi.fn(),
}));

const mockedApiRequest = vi.mocked(apiRequest);

describe('account location API', () => {
  beforeEach(() => {
    mockedApiRequest.mockReset();
  });

  it('loads owned locations and selects newest records first', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of locations.',
      data: [
        {
          id: 1,
          user_id: 10,
          latitude: 31.6295,
          longitude: -7.9811,
        },
        {
          id: 3,
          user_id: 10,
          latitude: 30.4278,
          longitude: -9.5981,
        },
      ],
    });

    await expect(
      accountLocationsRequest({
        token: 'test-token',
        userId: 10,
      }),
    ).resolves.toEqual([
      expect.objectContaining({ id: 3 }),
      expect.objectContaining({ id: 1 }),
    ]);

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/locations',
      {
        method: 'GET',
        token: 'test-token',
      },
    );
  });

  it('rejects an unexpected location owner', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'List of locations.',
      data: [
        {
          id: 1,
          user_id: 999,
          latitude: 31.6295,
          longitude: -7.9811,
        },
      ],
    });

    await expect(
      accountLocationsRequest({
        token: 'test-token',
        userId: 10,
      }),
    ).rejects.toThrow(
      'Account location does not belong to the authenticated user.',
    );
  });

  it('creates coordinates without sending user_id', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Location has been created.',
      data: {
        id: 4,
        user_id: 10,
        latitude: 31.6295,
        longitude: -7.9811,
      },
    });

    await createAccountLocationRequest({
      token: 'test-token',
      userId: 10,
      coordinates: {
        latitude: 31.6295,
        longitude: -7.9811,
      },
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/locations',
      {
        method: 'POST',
        token: 'test-token',
        body: JSON.stringify({
          latitude: 31.6295,
          longitude: -7.9811,
        }),
      },
    );
  });

  it('updates only the selected owned location coordinates', async () => {
    mockedApiRequest.mockResolvedValueOnce({
      success: true,
      message: 'Location has been updated successfully.',
      data: {
        id: 4,
        user_id: 10,
        latitude: 30.4278,
        longitude: -9.5981,
      },
    });

    await updateAccountLocationRequest({
      token: 'test-token',
      userId: 10,
      locationId: 4,
      coordinates: {
        latitude: 30.4278,
        longitude: -9.5981,
      },
    });

    expect(mockedApiRequest).toHaveBeenCalledWith(
      '/locations/4',
      {
        method: 'PUT',
        token: 'test-token',
        body: JSON.stringify({
          latitude: 30.4278,
          longitude: -9.5981,
        }),
      },
    );
  });
});
