import React from 'react';
import Hide from './Hide';
import View from './View';

export default function ViewPassword({ setViewPassword, viewPassword }) {
  return (
    <span
      className="relative float-right z-2 -mt-8 mr-4 bg-inherit"
      onClick={() => {
        setViewPassword(!viewPassword);
      }}
      as="button"
    >
      {viewPassword ? <Hide /> : <View />}
    </span>
  );
}
