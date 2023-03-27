import { combineReducers } from "redux";
import activeBoardId from "./activeBoardReducer";
import boards from "./boardsReducer";
import sections from "./sectionReducer";
import tasks from "./taskReducer";

const rootReducer = combineReducers({
  activeBoardId,
  sections,
  tasks,
  boards,
});

export default rootReducer;
