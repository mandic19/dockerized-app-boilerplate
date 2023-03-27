import { useRef } from "react";

const EMAIL_REGEX =
  /^[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+(?:.[a-zA-Z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/;

const useValidation = () => {
  const errors = useRef({});

  const validate = (form) => {
    errors.current = {};

    Object.entries(form?.fields).forEach(([key, field]) => {
      if (!field?.rules) return;

      field.rules.forEach((rule) => {
        if (typeof rule === "string") {
          const validatorFunc = getValidator(rule);

          validatorFunc(form, key, {});
        } else if (rule instanceof Object) {
          const { validator, props } = rule;

          const validatorFunc = getValidator(validator);

          validatorFunc(form, key, props);
        }
      });
    });

    const isValid = Object.keys(errors.current).length === 0;

    return { isValid, errors: errors.current };
  };

  const required = (form, key, { message = "This field is required." }) => {
    const val = getValueByKey(form, key);

    if ((typeof val === "string" && val.trim()) || val) return;

    errors.current[key] = message;
  };

  const minLength = (
    form,
    key,
    { min = 0, message = `This field must have at least ${min} characters.` }
  ) => {
    const value = getValueByKey(form, key);
    const length = value.length;

    if (min > length) {
      errors.current[key] = message;
    }
  };

  const maxLength = (
    form,
    key,
    { max = 0, message = `This field exceeds max length of ${max} characters.` }
  ) => {
    const value = getValueByKey(form, key);
    const length = value.length;

    if (max < length) {
      errors.current[key] = message;
    }
  };

  const compare = (
    form,
    key,
    {
      compareAttribute,
      message = `This field does not match ${compareAttribute}.`,
    }
  ) => {
    const value = getValueByKey(form, key);
    const compareValue = getValueByKey(form, compareAttribute);

    if (value !== compareValue) {
      errors.current[key] = message;
    }
  };

  const email = (form, key, { message = "Email format is invalid." }) => {
    const email = getValueByKey(form, key);

    if (!String(email).toLocaleLowerCase().match(EMAIL_REGEX)) {
      errors.current[key] = message;
    }
  };

  const getValidator = (name) => {
    const validators = [required, minLength, maxLength, email, compare];

    return validators.find((validator) => validator.name === name);
  };

  const getValueByKey = (form, key) => form?.fields[key]?.value ?? null;

  return validate;
};

export default useValidation;
