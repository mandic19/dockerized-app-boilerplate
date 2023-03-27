import * as types from "./actionTypes";
import * as sectionApi from "../../api/sectionApi";

export function loadSectionsSuccess(sections) {
  return { type: types.LOAD_SECTIONS_SUCCESS, sections };
}

export function createSectionSuccess(section) {
  return { type: types.CREATE_SECTION_SUCCESS, section };
}

export function updateSectionSuccess(section) {
  return { type: types.UPDATE_SECTION_SUCCESS, section };
}

export function reorderSectionSuccess(section) {
  return { type: types.REORDER_SECTION_SUCCESS, section };
}

export function deleteSectionSuccess(section) {
  return { type: types.DELETE_SECTION_SUCCESS, section };
}

export function invalidateSectionsState() {
  return { type: types.INVALIDATE_SECTIONS_STATE };
}

export function loadSections(params) {
  return function (dispatch) {
    return sectionApi
      .getSections(params)
      .then((sections) => {
        dispatch(loadSectionsSuccess(sections));
      })
      .catch((error) => {
        throw error;
      });
  };
}

export function createSection(params) {
  return function (dispatch) {
    return sectionApi
      .createSection(params)
      .then((section) => {
        dispatch(createSectionSuccess(section));
      })
      .catch((error) => {
        throw error;
      });
  };
}

export function updateSection(section, params) {
  return function (dispatch) {
    return sectionApi
      .updateSection(section.id, params)
      .then((section) => {
        dispatch(updateSectionSuccess(section));
      })
      .catch((error) => {
        throw error;
      });
  };
}

export function reorderSection(id, params) {
  return function (dispatch) {
    return sectionApi
      .reorderSection(id, params)
      .then((section) => {
        dispatch(reorderSectionSuccess(section));
      })
      .catch((error) => {
        throw error;
      });
  };
}

export function deleteSection(section, params) {
  return function (dispatch) {
    return sectionApi
      .deleteSection(section.id, params)
      .then(() => {
        dispatch(deleteSectionSuccess(section));
      })
      .catch((error) => {
        throw error;
      });
  };
}
