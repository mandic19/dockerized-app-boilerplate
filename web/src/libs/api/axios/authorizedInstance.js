import axios from "axios";
import authService from "../../services/authService";
import store from "../../redux/store";
import { setIsAuthentificated } from "../../redux/actions/authActions";
import { memoizedRefreshToken } from "../authApi";
import { env } from "../../../environments";

const instance = axios.create({
  baseURL: env.apiUrl,
});

instance.interceptors.request.use(
  async (config) => {
    const accessToken = authService.getLocalAccessToken();

    if (accessToken) {
      config.headers = {
        ...config.headers,
        Authorization: `Bearer ${accessToken}`,
      };
    }

    return config;
  },
  (error) => Promise.reject(error)
);

instance.interceptors.response.use(
  (response) => response,
  async (error) => {
    const config = error?.config;

    const refreshToken = authService.getLocalRefreshToken();

    if (!isUnauthorized(error) || config?.sent) {
      return Promise.reject(error);
    }

    config.sent = true;

    try {
      if (!refreshToken) throw new Error("Refresh token is missing !");

      const auth = await memoizedRefreshToken(refreshToken);
      authService.setAuthData(auth);
    } catch (error) {
      authService.removeAuthData();
      store.dispatch(setIsAuthentificated());
      return Promise.reject(error);
    }

    return instance(config);
  }
);

const isUnauthorized = (error) => {
  return error?.response?.status === 401;
};


export default instance;
