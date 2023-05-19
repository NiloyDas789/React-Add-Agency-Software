import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import ViewPassword from '@/Components/Global/ViewPassword';
import { useState } from 'react';

export default function Form({ data, setData, submit, errors, processing, isUpdating = 0 }) {
  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
  };
  const [viewOldPassword, setViewOldPassword] = useState(false);
  const [viewNewPassword, setViewNewPassword] = useState(false);
  const [viewConfirmPassword, setViewConfirmPassword] = useState(false);

  return (
    <form onSubmit={submit}>
      <div>
        <Label forInput="name" value="Name" required />
        <Input
          type="text"
          name="name"
          value={data.name}
          className="mt-1 block w-full"
          autoComplete="name"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.name} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="email" value="Email" required />
        <Input
          type="email"
          name="email"
          value={data.email}
          className="mt-1 block w-full"
          //   autoComplete="username"
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.email} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="old_password" value="Old Password" required />
        <Input
          type={viewOldPassword ? 'text' : 'password'}
          name="old_password"
          value={data.old_password}
          className="mt-1 block w-full"
          autoComplete="old_password"
          isFocused={true}
          handleChange={onHandleChange}
          required
        />
        <ViewPassword viewPassword={viewOldPassword} setViewPassword={setViewOldPassword} />
        <InputError message={errors.old_password} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="new_password" value="New Password" />
        <Input
          type={viewNewPassword ? 'text' : 'password'}
          name="new_password"
          value={data.new_password}
          className="mt-1 block w-full"
          autoComplete="new_password"
          isFocused={true}
          handleChange={onHandleChange}
        />
        <ViewPassword viewPassword={viewNewPassword} setViewPassword={setViewNewPassword} />
        <InputError message={errors.new_password} className="mt-2" />
      </div>

      <div className="mt-4">
        <Label forInput="confirm_password" value="Confirm Password" />
        <Input
          type={viewConfirmPassword ? 'text' : 'password'}
          name="confirm_password"
          value={data.confirm_password}
          className="mt-1 block w-full"
          autoComplete="confirm_password"
          isFocused={true}
          handleChange={onHandleChange}
        />
        <ViewPassword viewPassword={viewConfirmPassword} setViewPassword={setViewConfirmPassword} />
        <InputError message={errors.confirm_password} className="mt-2" />
      </div>

      <div className="flex items-center justify-end mt-4">
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
