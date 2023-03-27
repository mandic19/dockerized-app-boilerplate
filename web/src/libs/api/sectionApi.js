import axios from "./axios/guestInstance";
import { handleResponse, handleError } from "./apiUtils";

const baseUrl = "/sections";

export function getSections(params) {
  return axios
    .get(`${baseUrl}`, { params })
    .then(handleResponse)
    .catch(handleError);
}

export function getSection(id, params) {
  return axios
    .get(`${baseUrl}/${id}`, { params })
    .then(handleResponse)
    .catch(handleError);
}

export function createSection(params) {
  return axios
    .post(`${baseUrl}`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function updateSection(id, params) {
  return axios
    .put(`${baseUrl}/${id}`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function reorderSection(id, params) {
  return axios
    .put(`${baseUrl}/${id}/reorder`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function deleteSection(id, params) {
  return axios
    .delete(`${baseUrl}/${id}`, params)
    .then(handleResponse)
    .catch(handleError);
}
