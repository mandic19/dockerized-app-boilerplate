const SUCCESS_CODES = Array.from({ length: 100 }, (_, i) => 200 + i);

export async function handleResponse(response) {
  if (SUCCESS_CODES.includes(response.status)) {
    return response.data?.data ?? response.data;
  }

  return response;
}

export function handleError(error) {
  throw error;
}
