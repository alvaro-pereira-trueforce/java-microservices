import { InstagramModule } from './instagram.module';

describe('InstagramModule', () => {
  let instagramModule: InstagramModule;

  beforeEach(() => {
    instagramModule = new InstagramModule();
  });

  it('should create an instance', () => {
    expect(instagramModule).toBeTruthy();
  });
});
