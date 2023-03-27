import React, { useState } from "react";
import { connect } from "react-redux";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faPlus } from "@fortawesome/free-solid-svg-icons";
import { createSection } from "../../../../libs/redux/actions/sectionActions";
import InputInline from "../../../input-inline/InputInline";
import useForm from "../../../../libs/hooks/useForm";
import "./AddNewSection.css";

const AddNewSection = ({
  board_id,
  createSection,
  controlText = "Add a list",
}) => {
  const { form, setForm, resetForm, onChange, getParams, validate } = useForm({
    board_id: { rules: ["required"], value: board_id },
    name: { rules: ["required"] },
  });

  const [isActive, setIsActive] = useState(false);

  const onCancel = () => setIsActive(false);

  const onSubmit = (e) => {
    e.preventDefault();

    submitForm();
  };

  const submitForm = () => {
    const { isValid, errors } = validate(form);
    setForm({ ...form, isValid, errors, isSubmitted: true });

    if (isValid) {
      const params = getParams();

      createSection(params).then(() => {
        setIsActive(false);
        resetForm(true);
      });
    }
  };

  return (
    <div className="add-new-section">
      {isActive ? (
        <InputInline
          name="name"
          onBlur={onCancel}
          onSubmit={onSubmit}
          onCancel={onCancel}
          onChange={onChange}
          showSubmit={true}
          isTextarea={false}
          autoFocus={true}
          autoWidth={false}
          initialIsInFocus={true}
          submitText="Add list"
          placeholder="Enter a list title..."
          className="wrapper"
          value={form.fields.name.value}
        />
      ) : (
        <button
          className="btn no-hover"
          onClick={() => setIsActive((prev) => !prev)}
        >
          <FontAwesomeIcon icon={faPlus} className="mr-2" />
          {controlText}
        </button>
      )}
    </div>
  );
};

const mapStateToProps = () => {
  return {};
};

const mapDispatchToProps = {
  createSection,
};

export default connect(mapStateToProps, mapDispatchToProps)(AddNewSection);
