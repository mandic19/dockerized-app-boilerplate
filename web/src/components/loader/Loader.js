import React from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faSpinner } from "@fortawesome/free-solid-svg-icons";
import styles from "./loader.module.css";

const Loader = ({ size = "xl" }) => {
  return (
    <div className={styles.loader}>
      <FontAwesomeIcon className="m-auto" icon={faSpinner} size={size} spin />
    </div>
  );
};

export default Loader;
