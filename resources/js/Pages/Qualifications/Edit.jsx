import Heading from '@/Components/Global/Heading';
import Authenticated from '@/Layouts/Authenticated';
import { useForm } from '@inertiajs/inertia-react';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import Form from './Form';

export default function Edit({ auth, qualification }) {
  const { data, setData, put, processing, errors, reset } = useForm({
    title: qualification?.title || '',
    status: qualification?.status || 0,
  });

  useEffect(() => {
    return () => {
      reset();
    };
  }, []);

  const submit = (e) => {
    e.preventDefault();

    put(route('qualifications.update', qualification.id), {
      onSuccess: () => {
        toast.success('Qualification Updated successfully.');
      },
    });
  };

  return (
    <Authenticated auth={auth}>
      <div className="max-w-2xl mx-auto">
        <Heading className="mb-6">Edit Qualification</Heading>
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
