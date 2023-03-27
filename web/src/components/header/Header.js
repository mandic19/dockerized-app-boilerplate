import React from "react";
import styles from "./header.module.css";
import gridSvg from "../../assets/icons/grid.svg";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronDown } from "@fortawesome/free-solid-svg-icons";

const Header = () => {
  return (
    <header className={styles.header}>
      <button className="btn btn-transparent btn-icon-only">
        <img src={gridSvg} alt="grid" />
      </button>
      <button className={`btn btn-transparent ${styles.logo}`}></button>
      <div className={styles.nav}>
        <button className="btn btn-transparent">
          <span className="mr-1">Workspaces</span>
          <FontAwesomeIcon icon={faChevronDown} />
        </button>
        <button className="btn btn-transparent">
          <span className="mr-1">Recent</span>
          <FontAwesomeIcon icon={faChevronDown} />
        </button>
        <button className="btn btn-transparent">
          <span className="mr-1">Starred</span>
          <FontAwesomeIcon icon={faChevronDown} />
        </button>
        <button className="btn btn-transparent">
          <span className="mr-1">Templates</span>
          <FontAwesomeIcon icon={faChevronDown} />
        </button>
      </div>
    </header>
  );
};

export default Header;
