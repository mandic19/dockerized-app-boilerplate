export const getParams = (oldOrder, newOrder) => {
  oldOrder = parseInt(oldOrder);
  newOrder = parseInt(newOrder);
  const increment = oldOrder >= newOrder ? 1 : -1;
  const bottomLimit = increment > 0 ? newOrder : oldOrder - increment;
  const topLimit = increment < 0 ? newOrder : oldOrder - increment;

  return { increment, bottomLimit, topLimit };
};
