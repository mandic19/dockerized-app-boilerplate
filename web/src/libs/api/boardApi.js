import axios from "./axios/guestInstance";
import { handleResponse, handleError } from "./apiUtils";

const baseUrl = "/boards";

export function getBoards(params) {
  return axios
    .get(`${baseUrl}`, { params })
    .then(handleResponse)
    .catch(handleError);
}

export function getBoard(id, params) {
  return axios
    .get(`${baseUrl}/${id}`, { params })
    .then(handleResponse)
    .catch(handleError);
}

export function createBoard(params) {
  return axios
    .post(`${baseUrl}`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function updateBoard(id, params) {
  return axios
    .put(`${baseUrl}/${id}`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function deleteBoard(id, params) {
  return axios
    .delete(`${baseUrl}/${id}`, params)
    .then(handleResponse)
    .catch(handleError);
}
