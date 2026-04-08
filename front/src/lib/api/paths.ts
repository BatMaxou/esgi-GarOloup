export const apiPaths = {
  game: {
    getCurrent: '/game',
    create: '/games',
    join: '/game/join',
    close: '/game/invitation/close',
    open: '/game/invitation/open',
    setConfiguration: '/game/configuration',
    roleDispatch: '/game/role-dispatch',
    launch: '/game/launch',
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
  role: {
    list: '/roles',
    get: (id: string) => `/roles/${id}`,
    update: (id: string) => `/roles/${id}`,
    updateFiles: (id: string) => `/roles/${id}/files`,
  },
  filter: {
    gameTeam: '/filters/game_teams',
  },
  mercure: {
    credentials: '/mercure/token',
  },
};
