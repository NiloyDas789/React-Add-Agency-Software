export const getResponseErrors = (res) => {
  let errors = '';
  if (res?.errors) {
    Object.values(res?.errors).map((error) => {
      errors += error[0] + '\n';
    });
  } else if (res?.message) {
    errors = res.message;
  }
  return errors;
};
