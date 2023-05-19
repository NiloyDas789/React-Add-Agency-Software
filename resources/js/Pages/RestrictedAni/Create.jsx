import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Create({ auth }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    restricted_ani: '',
    date: '',
    reason: '',
    status: 1,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    post(route('restricted-ani.store'), {
      preserveScroll: true,
      onSuccess: () => {
        reset();
        toast.success('Restricted Ani created successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Create Restricted Ani</Heading>
        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
        />
      </div>
    </Authenticated>
  );
}
