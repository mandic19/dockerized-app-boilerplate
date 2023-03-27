import initialState from "./initialState";
import * as types from "../actions/actionTypes";

export default function taskReducer(state = initialState.tasks, action) {
  switch (action.type) {
    case types.LOAD_TASKS_SUCCESS:
      return [...state, ...action.tasks];
    case types.CREATE_TASK_SUCCESS:
      return [...state, action.task];
    case types.UPDATE_TASK_SUCCESS:
    case types.REORDER_TASK_SUCCESS:
      return state.map((task) =>
        task.id !== action.task.id ? task : action.task
      );
    case types.DELETE_TASK_SUCCESS:
      return state.filter((task) => task.id !== action.task.id);
    default:
      return state;
  }
}
