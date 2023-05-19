const request = async (url, data = {}) => {
  return await fetch(url, {
    method: 'POST',
    body: JSON.stringify(data),
  })
    .then((res) => res.json())
    .then((res) => res)
    .catch((err) => console.error(err));
};

export default request;
