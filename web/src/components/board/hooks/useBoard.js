import { useState } from "react";
import { useRef } from "react";
import { useEffect } from "react";
import * as reoderHelper from "../../../libs/helpers/reoderHelper";
import useForm from "../../../libs/hooks/useForm";

const useBoard = ({
  board,
  sections,
  updateBoard,
  reorderSection,
  reorderTask,
  invalidateSectionsState,
}) => {
  const isMounted = useRef(false);

  const { form, setForm, resetForm, onChange, getParams, validate } = useForm({
    name: { rules: ["required"], value: board.name },
  });

  const [boardSections, setBoardSections] = useState(sections);

  useEffect(() => {
    if (isMounted.current) {
      setBoardSections(sections);
    }
  }, [sections]);

  useEffect(() => {
    isMounted.current = true;
    return () => {
      invalidateSectionsState();
    };
  }, []);

  const onSubmit = (e) => {
    e.preventDefault();

    submitForm();
  };

  const onBlur = () => submitForm();

  const submitForm = () => {
    const { isValid, errors } = validate(form);

    if (isValid) {
      const params = getParams();

      updateBoard(board, params).then(() => resetForm());
    } else {
      resetForm(true);
    }

    setForm({ ...form, isValid, errors, isSubmitted: true });
  };

  const onSectionDragEnd = (res) => {
    console.log(res);
    // dropped outside the list
    if (!res.destination) {
      return;
    }

    const srcIndex = res.source.index;
    const destIndex = res.destination.index;

    if (srcIndex === destIndex) return;

    const sectionId = parseInt(res.draggableId);
    const order = destIndex;

    const { increment, bottomLimit, topLimit } = reoderHelper.getParams(
      srcIndex,
      destIndex
    );

    setBoardSections((prev) =>
      prev
        .map((s) => {
          if (s.id === sectionId) {
            return { ...s, order };
          } else if (s.order >= bottomLimit && s.order <= topLimit) {
            return { ...s, order: s.order + increment };
          }

          return s;
        })
        .sort((a, b) => a.order - b.order)
    );

    reorderSection(sectionId, { order });
  };

  const onTaskDragEnd = (res) => {
    // dropped outside the list
    if (!res.destination) {
      return;
    }

    const { destination, source, draggableId } = res;

    if (
      source.index === destination.index &&
      source.droppableId === destination.droppableId
    )
      return;

    const taskId = parseInt(draggableId);
    const order = destination.index;
    const section_id = parseInt(
      destination.droppableId.replace("section-droppable-", "")
    );

    reorderTask(taskId, { order, section_id });
  };

  return {
    board,
    boardSections,
    form,
    onChange,
    onSubmit,
    onBlur,
    onSectionDragEnd,
    onTaskDragEnd,
  };
};

export default useBoard;
