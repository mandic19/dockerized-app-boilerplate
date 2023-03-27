import React from "react";
import "./Task.css";

const Task = ({ task }) => {
  return (
    <div className="task">
      <div className="content">{task.name}</div>
      <div className="details"></div>
    </div>
  );
};

export default Task;
