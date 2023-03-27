import axios from "axios";
import { env } from "../../../environments";

const instance = axios.create({
  baseURL: env.apiUrl,
});

export default instance;
