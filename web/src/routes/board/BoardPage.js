import React, { useEffect, useState } from "react";
import { connect } from "react-redux";
import { useNavigate, useParams } from "react-router-dom";
import { loadBoard } from "../../libs/redux/actions/boardActions";
import Header from "../../components/header/Header";
import Loader from "../../components/loader/Loader";
import Board from "../../components/board/Board";
import "./BoardPage.css";

const BoardPage = ({ board, loadBoard }) => {
  const [isLoading, setIsLoading] = useState(true);
  const { board_id } = useParams();
  const navigate = useNavigate();

  useEffect(() => {
    loadBoard(board_id)
      .then(() => setIsLoading(false))
      .catch(() => navigate("/page-not-found", { replace: true }));
  }, []);

  return (
    <>
      <Header />
      <div className="board-page">
        {isLoading ? <Loader size="2x" /> : <Board board={board} />}
      </div>
    </>
  );
};

const mapStateToProps = (state) => {
  return {
    board: state.boards.find((board) => board.id === state.activeBoardId),
  };
};

const mapDispatchToProps = {
  loadBoard,
};

export default connect(mapStateToProps, mapDispatchToProps)(BoardPage);
