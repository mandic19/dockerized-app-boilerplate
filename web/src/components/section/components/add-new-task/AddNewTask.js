import React, { useState } from "react";
import { connect } from "react-redux";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { createTask } from "../../../../libs/redux/actions/taskActions";
import InputInline from "../../../input-inline/InputInline";
import useForm from "../../../../libs/hooks/useForm";
import "./AddNewTask.css";

const AddNewTask = ({ section_id, createTask }) => {
  const { form, setForm, resetForm, onChange, getParams, validate } = useForm({
    section_id: { rules: ["required"], value: section_id },
    name: { rules: ["required"] },
  });

  const [isActive, setIsActive] = useState(false);

  const onCancel = () => setIsActive(false);

  const onSubmit = (e) => {
    e.preventDefault();

    submitForm();
  };

  const onBlur = () => submitForm();

  const submitForm = async () => {
    const { isValid, errors } = validate(form);
    setForm({ ...form, isValid, errors, isSubmitted: true });

    if (isValid) {
      const params = getParams();

      await createTask(params);

      resetForm(true);
    }

    setIsActive(false);
  };

  return (
    <div className="add-new-task">
      {isActive ? (
        <InputInline
          name="name"
          onBlur={onBlur}
          onSubmit={onSubmit}
          onCancel={onCancel}
          onChange={onChange}
          showSubmit={true}
          isTextarea={true}
          autoFocus={true}
          autoWidth={false}
          initialIsInFocus={true}
          submitText="Add card"
          placeholder="Enter a title for this card..."
          className="wrapper"
          rows={3}
        />
      ) : (
        <button
          className="btn btn-neutral"
          onClick={() => setIsActive((prev) => !prev)}
        >
          <FontAwesomeIcon icon={faPlus} className="mr-2" />
          Add a card
        </button>
      )}
    </div>
  );
};

const mapStateToProps = () => {
  return {};
};

const mapDispatchToProps = {
  createTask,
};

export default connect(mapStateToProps, mapDispatchToProps)(AddNewTask);
