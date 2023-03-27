import React from "react";
import { Route, Routes } from "react-router-dom";
import BoardPage from "./routes/board/BoardPage";
import PageNotFound from "./routes/PageNotFound";

function App() {
  return (
    <Routes>
      <Route path="/board/:board_id" element={<BoardPage />} />
      <Route path="*" element={<PageNotFound />} />
    </Routes>
  );
}

export default App;
