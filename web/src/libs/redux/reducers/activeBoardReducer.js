import initialState from "./initialState";
import * as types from "../actions/actionTypes";

export default function activeBoardReducer(
  state = initialState.activeBoardId,
  action
) {
  switch (action.type) {
    case types.LOAD_BOARD_SUCCESS:
      return action.board.id;
    default:
      return state;
  }
}
