import { useState } from "react";
import useValidation from "./useValidation";

const useForm = (fields = {}) => {
  const validate = useValidation();

  const initialFormState = {
    fields: getFormattedFields(fields),
    errors: {},
    isSubmitted: false,
    isValid: false,
  };

  const [isSubmitting, setIsSubmitting] = useState(false);
  const [form, setForm] = useState(initialFormState);
  const isSubmitDisabled = isSubmitting || (form.isSubmitted && !form.isValid);

  const onChange = (e) => {
    const { name, value, type, files } = e.target;

    const updatedForm = {
      ...form,
      fields: {
        ...form.fields,
        ...{
          [name]: {
            ...form.fields[name],
            value: type === "file" ? files[0] : value,
            isTouched: true,
          },
        },
      },
    };

    const { isValid, errors } = validate(updatedForm);

    setForm({ ...updatedForm, isValid, errors });
  };

  const onSubmit = () => {
    const { isValid, errors } = validate(form);

    setForm({ ...form, isValid, errors, isSubmitted: true });
  };

  const isInvalidField = (key) => {
    const field = form.fields[key];
    const error = form.errors[key];

    return (field.isTouched && error) || (form.isSubmitted && error);
  };

  const resetForm = (initialState = false) => {
    initialState
      ? setForm(initialFormState)
      : setForm({
          ...initialFormState,
          fields: getFormattedFields(form.fields),
        });
  };

  const getParams = () => {
    const params = {};

    Object.entries(form.fields).forEach(([key, field]) => {
      params[key] = field.value ?? null;
    });

    return params;
  };

  return {
    form,
    setForm,
    resetForm,
    getParams,
    onChange,
    onSubmit,
    isInvalidField,
    isSubmitDisabled,
    isSubmitting,
    setIsSubmitting,
    validate,
  };
};

const getFormattedFields = (fields) => {
  const formattedFields = {};

  Object.entries(fields).forEach(([key, field]) => {
    formattedFields[key] = { ...{ value: "", isTouched: false }, ...field };
  });

  return formattedFields;
};

export default useForm;
