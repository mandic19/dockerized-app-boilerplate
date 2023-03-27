import axios from "./axios/guestInstance";
import { handleResponse, handleError } from "./apiUtils";

const baseUrl = "/tasks";

export function getTasks(params) {
  return axios
    .get(`${baseUrl}`, { params })
    .then(handleResponse)
    .catch(handleError);
}

export function getTask(id, params) {
  return axios
    .get(`${baseUrl}/${id}`, { params })
    .then(handleResponse)
    .catch(handleError);
}

export function createTask(params) {
  return axios
    .post(`${baseUrl}`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function upadteTask(id, params) {
  return axios
    .put(`${baseUrl}/${id}`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function reorderTask(id, params) {
  return axios
    .put(`${baseUrl}/${id}/reorder`, params)
    .then(handleResponse)
    .catch(handleError);
}

export function deleteTask(id, params) {
  return axios
    .delete(`${baseUrl}/${id}`, params)
    .then(handleResponse)
    .catch(handleError);
}
