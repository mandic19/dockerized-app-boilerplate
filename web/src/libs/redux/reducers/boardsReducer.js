import initialState from "./initialState";
import * as types from "../actions/actionTypes";

export default function boardsReducer(state = initialState.boards, action) {
  switch (action.type) {
    case types.LOAD_BOARDS_SUCCESS:
      return [...state, ...action.boards];
    case types.LOAD_BOARD_SUCCESS:
      return [...state, action.board];
    case types.CREATE_BOARD_SUCCESS:
      return [...state, action.board];
    case types.UPDATE_BOARD_SUCCESS:
      return state.map((board) =>
        board.id !== action.board.id ? board : action.board
      );
    case types.DELETE_BOARD_SUCCESS:
      return state.filter((board) => board.id !== action.board.id);
    default:
      return state;
  }
}
