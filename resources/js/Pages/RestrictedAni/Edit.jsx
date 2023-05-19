import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, restrictedAni }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    restricted_ani: restrictedAni?.restricted_ani || '',
    date: restrictedAni?.date || '',
    reason: restrictedAni?.reason || '',
    status: restrictedAni?.status || 0,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('restricted-ani.update', restrictedAni.id), {
      onSuccess: () => {
        toast.success('Restricted Ani updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit Restricted Ani</Heading>
        <Form
          data={data}
          setData={setData}
          submit={submit}
          errors={errors}
          processing={processing}
          isUpdating={true}
        />
      </div>
    </Authenticated>
  );
}
