import { useEffect, useRef } from 'react';

export default function Input({
  type = 'text',
  name,
  value,
  className = '',
  autoComplete,
  required,
  isFocused,
  handleChange,
  readOnly = false,
  ...rest
}) {
  const input = useRef();

  useEffect(() => {
    if (isFocused) {
      input.current.focus();
    }
  }, []);

  return (
    <input
      type={type}
      name={name}
      value={value}
      className={
        (readOnly ? 'bg-gray-100 border-gray-300 text-gray-500' : '') +
        ` border-slate-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 ` +
        className
      }
      ref={input}
      autoComplete={autoComplete}
      required={required}
      onChange={(e) => handleChange(e)}
      readOnly={readOnly}
      aria-label="Input"
      {...rest}
    />
  );
}
