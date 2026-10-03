const TOKEN_KEY = 'petmingle.auth.token';

export const tokenStorage = {
  get(): string | null {
    return window.localStorage.getItem(TOKEN_KEY);
  },

  set(token: string): void {
    window.localStorage.setItem(TOKEN_KEY, token);
  },

  clear(): void {
    window.localStorage.removeItem(TOKEN_KEY);
  },
};