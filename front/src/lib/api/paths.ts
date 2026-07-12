export const apiPaths = {
  game: {
    getCurrent: '/game',
    getPublics: (page: number = 1, itemsPerPage?: number) => {
      const params = new URLSearchParams({ page: String(page) });
      if (itemsPerPage !== undefined) params.set('itemsPerPage', String(itemsPerPage));
      return `/games/public?${params.toString()}`;
    },
    create: '/games',
    join: '/game/join',
    close: '/game/invitation/close',
    open: '/game/invitation/open',
    setConfiguration: '/game/configuration',
    resetConfiguration: '/game/configuration/reset',
    setGameMaster: '/game/game-master',
    resetGameMaster: '/game/game-master/reset',
    roleDispatch: '/game/role-dispatch',
    resetRoleDispatch: '/game/role-dispatch/reset',
    launch: '/game/launch',
    vote: '/game/vote',
    timeUp: '/game/time-up',
    passTurn: '/game/pass-turn',
  },
  seer: {
    reveal: '/game/seer/reveal',
  },
  witch: {
    save: '/game/witch/save',
    poison: '/game/witch/poison',
  },
  wildChild: {
    setup: '/game/wild-child/setup',
  },
  infectFather: {
    infect: '/game/infect-father/infect',
  },
  hunter: {
    shoot: '/game/hunter/shoot',
  },
  cupidon: {
    setup: '/game/cupidon/setup',
  },
  assassin: {
    kill: '/game/assassin/kill',
  },
  player: {
    getCurrent: '/game/player',
    leave: '/game/player',
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
    create: '/roles',
    get: (id: string) => `/roles/${id}`,
    update: (id: string) => `/roles/${id}`,
    updateFiles: (id: string) => `/roles/${id}/files`,
  },
  filter: {
    gameTeam: '/filters/game_teams',
  },
  homepage: '/homepage',
  mercure: {
    credentials: '/mercure/token',
  },
  werewolf: {
    team: '/game/werewolf/team',
    vote: '/game/werewolf/vote',
  },
  recap: {
    getByGame: (gameId: string) => `/games/${gameId}/recap`,
  },
};
