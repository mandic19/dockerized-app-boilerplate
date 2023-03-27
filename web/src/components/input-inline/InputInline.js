import React, { useEffect, useState } from "react";
import { useRef } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faTimes } from "@fortawesome/free-solid-svg-icons";
import styles from "./input.module.css";

const ENTER_KEY = 13;

const InputInline = ({
  value,
  onCancel,
  onSubmit,
  onChange,
  onBlur,
  className,
  initialIsInFocus = false,
  autoWidth = true,
  isTextarea = false,
  showSubmit = false,
  submitText = "Submit",
  ...props
}) => {
  const isMounted = useRef(false);
  const formRef = useRef(null);
  const inputRef = useRef(null);

  const [inputValue, setInputValue] = useState(value);
  const [isInFocus, setIsInFocus] = useState(initialIsInFocus);

  useEffect(() => {
    setInputValue(value);
  }, [value]);

  useEffect(() => {
    if (!isMounted.current || isInFocus) return;

    if (typeof onBlur === "function") {
      onBlur();
    }
  }, [isInFocus]);

  useEffect(() => {
    isMounted.current = true;
  }, []);

  useEffect(() => {
    document.addEventListener("mousedown", outOfFocusHandler);
    return () => {
      document.removeEventListener("mousedown", outOfFocusHandler);
    };
  }, [formRef]);

  const outOfFocusHandler = (e) => {
    setIsInFocus(formRef.current && formRef.current.contains(e.target));
  };

  const wrapperClassName = `${styles.wrapper} ${className ? className : ""}`;

  const handleOnChange = (e) => {
    setInputValue(e.target.value);
    onChange(e);
  };

  const handleOnKeyDown = (e) => {
    if (e.which === ENTER_KEY) {
      onSubmit(e);
      inputRef.current.blur();
    }
  };

  const getInputProps = () => {
    const inputProps = {
      ref: inputRef,
      onChange: handleOnChange,
      onKeyDown: handleOnKeyDown,
      className: styles.input,
      value: inputValue,
      ...props,
    };

    if (isTextarea) {
      inputProps.className += ` ${styles.textarea}`;
    }

    return inputProps;
  };

  return (
    <form ref={formRef} onSubmit={onSubmit}>
      <div className={wrapperClassName}>
        {isTextarea ? (
          <textarea {...getInputProps()} />
        ) : (
          <input {...getInputProps()} />
        )}
        {autoWidth && <span className={styles.ghost}>{inputValue}</span>}
      </div>
      {showSubmit && (
        <div className="mt-1">
          <input type="submit" value={submitText} className="btn btn-primary" />
          <button type="button" className="btn btn-link" onClick={onCancel}>
            <FontAwesomeIcon icon={faTimes} size="xl" />
          </button>
        </div>
      )}
    </form>
  );
};

export default InputInline;
