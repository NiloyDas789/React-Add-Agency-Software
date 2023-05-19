import Button from '@/Components/Global/Button';
import Modal from '@/Components/Global/Modal';

export default function DeleteModal({ isOpenModal, setIsOpenModal, handleDelete, processing }) {
  return (
    <Modal isOpen={isOpenModal} close={setIsOpenModal}>
      <h2 className="font-semibold text-xl text-center mb-6">Are you sure?</h2>
      <div className="flex justify-center gap-4">
        <Button
          onClick={() => setIsOpenModal(false)}
          className="bg-slate-700 text-white active:bg-slate-300"
        >
          Cancel
        </Button>
        <Button
          onClick={handleDelete}
          processing={processing}
          className="bg-red-500 text-white active:bg-red-700"
        >
          Delete
        </Button>
      </div>
    </Modal>
  );
}
