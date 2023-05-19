import Button from '@/Components/Global/Button';
import Input from '@/Components/Global/Input';
import InputError from '@/Components/Global/InputError';
import Label from '@/Components/Global/Label';

export default function Form({ data, setData, submit, errors, processing, isUpdating = 0 }) {
  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
  };

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
          handleChange={onHandleChange}
          required
        />
        <InputError message={errors.email} className="mt-2" />
      </div>

      <div className="flex items-center justify-end mt-4">
        <Button className="ml-4" processing={processing}>
          {isUpdating ? 'Update' : 'Create'}
        </Button>
      </div>
    </form>
  );
}
