import initialState from "./initialState";
import * as types from "../actions/actionTypes";
import * as reoderHelper from "../../helpers/reoderHelper";

export default function sectionReducer(state = initialState.sections, action) {
  switch (action.type) {
    case types.LOAD_SECTIONS_SUCCESS:
      return action.sections;
    case types.CREATE_SECTION_SUCCESS:
      return [...state, action.section];
    case types.UPDATE_SECTION_SUCCESS:
    case types.REORDER_SECTION_SUCCESS:
      const section = state.find((x) => x.id === action.section.id);

      const { increment, bottomLimit, topLimit } = reoderHelper.getParams(
        section.order,
        action.section.order
      );

      return state
        .map((s) => {
          if (s.id === action.section.id) {
            return action.section;
          } else if (s.order >= bottomLimit && s.order <= topLimit) {
            return { ...s, order: s.order + increment };
          }

          return s;
        })
        .sort((a, b) => a.order - b.order);
    case types.DELETE_SECTION_SUCCESS:
      return state.filter((section) => section.id !== action.section.id);
    default:
      return state;
  }
}
