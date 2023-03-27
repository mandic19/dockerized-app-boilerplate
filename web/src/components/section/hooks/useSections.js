import { useEffect, useState } from "react";
import useForm from "../../../libs/hooks/useForm";

const useSection = ({
  section,
  tasks,
  updateSection,
  loadTasks,
  invalidateTasksState,
}) => {
  const [isLoading, setIsLoading] = useState(true);

  const { form, setForm, onChange, getParams, resetForm, validate } = useForm({
    board_id: { rules: ["required"], value: section.board_id },
    name: { rules: ["required"], value: section.name },
  });

  useEffect(() => {
    loadTasks({ section_id: section.id }).then(() => setIsLoading(false));

    return () => {
      invalidateTasksState();
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

      updateSection(section, params).then(() => {
        resetForm();
      });
    } else {
      resetForm(true);
    }

    setForm({ ...form, isValid, errors, isSubmitted: true });
  };

  return {
    section,
    tasks,
    form,
    isLoading,
    onChange,
    onSubmit,
    onBlur,
  };
};

export default useSection;
