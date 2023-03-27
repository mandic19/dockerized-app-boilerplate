import { createStore, applyMiddleware, compose } from "redux";
import rootReducer from "./reducers";
import thunk from "redux-thunk";

 const configureStore = (initialState) => {
  const composeEnhacers =
    window.__REDUX_DEVTOOLS_EXTENSION_COMPOSE__ || compose; // Add support for Redux dev tools

  return createStore(
    rootReducer,
    initialState,
    composeEnhacers(applyMiddleware(thunk))
  );
}

const store = configureStore();

export default store;