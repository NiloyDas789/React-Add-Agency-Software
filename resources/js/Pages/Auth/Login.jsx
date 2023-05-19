import React, { useEffect, useState } from 'react';
import Button from '@/Components/Global/Button';
import Checkbox from '@/Components/Global/Checkbox';
import Guest from '@/Layouts/Guest';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';
import { Head, Link, useForm } from '@inertiajs/inertia-react';
import ViewPassword from '@/Components/Global/ViewPassword';

export default function Login({ status, canResetPassword }) {
  const [viewPassword, setViewPassword] = useState(false);
  const { data, setData, post, processing, errors, reset } = useForm({
    email: '',
    password: '',
    remember: '',
  });

  useEffect(() => {
    return () => {
      reset('password');
    };
  }, []);

  const onHandleChange = (event) => {
    setData(
      event.target.name,
      event.target.type === 'checkbox' ? event.target.checked : event.target.value
    );
  };

  const submit = (e) => {
    e.preventDefault();

    post(route('login'));
  };

  return (
    <Guest>
      <Head title="Log in" />

      {status && <div className="mb-4 font-medium text-sm text-green-600">{status}</div>}

      <form onSubmit={submit}>
        <div>
          <Label forInput="email" value="Email" />

          <Input
            type="text"
            name="email"
            value={data.email}
            className="mt-1 block w-full"
            autoComplete="username"
            isFocused={true}
            handleChange={onHandleChange}
          />

          <InputError message={errors.email} className="mt-2" />
        </div>

        <div className="mt-4">
          <Label forInput="password" value="Password" />

          <Input
            type={viewPassword ? 'text' : 'password'}
            name="password"
            value={data.password}
            className="mt-1 block w-full"
            autoComplete="current-password"
            handleChange={onHandleChange}
          />
          <ViewPassword viewPassword={viewPassword} setViewPassword={setViewPassword} />

          <InputError message={errors.password} className="mt-2" />
        </div>

        <div className="block mt-4">
          <label className="flex items-center">
            <Checkbox name="remember" checked={data.remember} handleChange={onHandleChange} />

            <span className="ml-2 text-sm text-slate-600">Remember me</span>
          </label>
        </div>

        <div className="flex items-center justify-end mt-4">
          {canResetPassword && (
            <Link
              href={route('password.request')}
              className="underline text-sm text-slate-600 hover:text-slate-900"
            >
              Forgot your password?
            </Link>
          )}

          <Button className="ml-4" processing={processing}>
            Log in
          </Button>
        </div>
      </form>
    </Guest>
  );
}
