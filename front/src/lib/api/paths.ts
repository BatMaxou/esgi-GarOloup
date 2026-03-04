export const apiPaths = {
  game: {
    getCurrent: '/game',
    create: '/games',
    join: '/game/join',
    close: '/game/invtation/close',
  },
  player: {
    getCurrent: '/game/player',
  },
  tempUser: {
    get: (username: string) => `/temp_user?username=${username}`,
  },
  user: {
    me: '/me',
    register: '/register',
    forgotPassword: '/forgot_password',
    resetPassword: '/reset_password',
  },
  login: '/login',
  refreshToken: '/token/refresh',
};
