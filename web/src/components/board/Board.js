import React from "react";
import { connect } from "react-redux";
import { updateBoard } from "../../libs/redux/actions/boardActions";
import { invalidateSectionsState } from "../../libs/redux/actions/sectionActions";
import { reorderSection } from "../../libs/redux/actions/sectionActions";
import { reorderTask } from "../../libs/redux/actions/taskActions";
import InputInline from "../input-inline/InputInline";
import Section from "../section/Section";
import useBoard from "./hooks/useBoard";
import "./Board.css";
import AddNewSection from "./components/add-new-section/AddNewSection";
import { DragDropContext, Draggable, Droppable } from "react-beautiful-dnd";

const Board = (props) => {
  const {
    board,
    boardSections,
    form,
    onChange,
    onSubmit,
    onBlur,
    onSectionDragEnd,
    onTaskDragEnd,
  } = useBoard(props);

  return (
    <div className="board">
      <div className="header">
        <div className="title">
          <InputInline
            name="name"
            onSubmit={onSubmit}
            onBlur={onBlur}
            onChange={onChange}
            className="input-inline-wrapper px-2"
            value={form.fields.name.value}
          />
        </div>
      </div>
      <DragDropContext onDragEnd={onSectionDragEnd}>
        <Droppable
          droppableId={`board-droppable-${board.id}`}
          direction="horizontal"
        >
          {(provided, snapshot) => (
            <div
              ref={provided.innerRef}
              // style={getListStyle(snapshot.isDraggingOver)}
              className="body"
            >
              <DragDropContext onDragEnd={onTaskDragEnd}>
                {boardSections.map((section) => (
                  <Draggable
                    key={section.id}
                    draggableId={`${section.id}`}
                    index={parseInt(section.order)}
                  >
                    {(provided, snapshot) => (
                      <div
                        ref={provided.innerRef}
                        {...provided.draggableProps}
                        {...provided.dragHandleProps}
                      >
                        <Section
                          section={section}
                          isDragging={snapshot.isDragging}
                        />
                      </div>
                    )}
                  </Draggable>
                ))}
              </DragDropContext>
              {provided.placeholder}
              <div className="add-section-wrapper">
                <AddNewSection
                  board_id={board.id}
                  controlText={
                    boardSections.length > 0 ? "Add another list" : "Add a list"
                  }
                />
              </div>
            </div>
          )}
        </Droppable>
      </DragDropContext>
    </div>
  );
};

const mapStateToProps = (state) => {
  return {
    sections: state.sections,
  };
};

const mapDispatchToProps = {
  updateBoard,
  reorderSection,
  reorderTask,
  invalidateSectionsState,
};

export default connect(mapStateToProps, mapDispatchToProps)(Board);
